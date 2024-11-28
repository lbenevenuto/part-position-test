# Assumptions

The easiest way to recalculate the position of the parts and still have consistency in the database is using PostgreSQL with DEFERRABLE unique CONSTRAINTS.  
For MySQL: **InnoDB checks foreign key constraints immediately; the check is not deferred to transaction commit.** So we CAN'T use MySQL.

### Considerations for Scalability
Ensure all updates are transactional to prevent data corruption during concurrent operations.
Add a composite unique index on (episode_id, position) to enforce data integrity.
This approach is efficient, straightforward, and ensures consistent position recalculations.
```sql
ALTER TABLE parts DROP CONSTRAINT IF EXISTS parts_episode_id_position_unique, ADD CONSTRAINT parts_episode_id_position_unique UNIQUE (episode_id, position) DEFERRABLE INITIALLY DEFERRED;
```

### Using Observers
#### Explanation of the Observer Methods
1. Creating Method:
   - Adjusts positions when a new Part is inserted.
   - Shifts all Parts with position >= newPart.position to make space for the new Part.
2. Updating Method:
   - Handles position changes for existing Parts.
   - Moves affected Parts up or down depending on whether the new position is greater or less than the original position.
3. Deleting Method:
   - Adjusts positions when a Part is deleted.
   - Decrements the position of all subsequent Parts to close the gap.

### Advantages of This Approach
1. Centralized Logic: All position recalculations are encapsulated in the PartObserver, making the model more cohesive and maintaining the DRY principle.
2. Database Integrity: The unique index on episode_id and position ensures no duplicate positions.
3. Atomic Operations: Transactions ensure that changes are applied consistently across all affected rows.


## Alternative Approach: Gapped Indexing

Instead of recalculating positions every time, use a gapped sequence (e.g., 10, 20, 30). When adding a new part, insert it into the gap (e.g., 15 between 10 and 20). Recalculation happens only when gaps are exhausted.

Benefits:

- Minimizes updates to the database.
- Scales well for large datasets with frequent edits.


### You can use the `Insomnia_requests.json` file to load all REST API requests inside Insomnia and see all the endpoints working with the payload.
![image](https://github.com/user-attachments/assets/7e5c075e-3d62-4d62-b2d5-511f254a0192)

# Requirements
1. Git
2. Docker
3. Docker Compose


## How to run the project

```shell
# Clone the repository
git clone
cp .env.example .env
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
# Change the env variables in the .env file
# DB_CONNECTION=pgsql
# DB_HOST=pgsql
# DB_PORT=5432
# DB_DATABASE=laravel
# DB_USERNAME=sail
# DB_PASSWORD=password
# SESSION_DRIVER=redis
# QUEUE_CONNECTION=redis
# MAIL_MAILER=smtp
# MAIL_HOST=mailpit
# MAIL_PORT=1025
# And TELESCOPE_ENABLED=true if you want to use Telescope
./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate:fresh --seed
```

### After running the above commands, you can access the project at http://localhost
#### To retrieve the episodes
```shell
curl --request GET \
  --url http://localhost/api/episodes \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json'
```
### To retrieve the parts
```shell
curl --request GET \
  --url http://localhost/api/parts \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json'
```
### To retrieve parts from episode 1
```shell
curl --request GET \
  --url http://localhost/api/episodes/1/parts \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json'
```
### To duplicate an episode. Change **{EPISODE_ID}** with the episode id you want to duplicate
```shell
curl --request POST \
  --url http://localhost/api/episodes/{EPISODE_ID}/duplicate \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json'
```

## how to run the tests
```shell
./vendor/bin/sail artisan test
```

