#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

source /usr/local/bin/bootstrap-app-env.sh

echo "Waiting for MySQL to accept connections..."
for attempt in $(seq 1 60); do
  if php -r '
    $host = getenv("DB_HOST") ?: "127.0.0.1";
    $port = getenv("DB_PORT") ?: "3306";
    $database = getenv("DB_DATABASE") ?: "";
    $username = getenv("DB_USERNAME") ?: "";
    $password = getenv("DB_PASSWORD") ?: "";

    try {
        new PDO(
            sprintf("mysql:host=%s;port=%s;dbname=%s", $host, $port, $database),
            $username,
            $password,
            [PDO::ATTR_TIMEOUT => 3]
        );
        exit(0);
    } catch (Throwable $e) {
        fwrite(STDERR, $e->getMessage() . PHP_EOL);
        exit(1);
    }
  '; then
    echo "MySQL is ready."
    break
  fi

  if [[ "$attempt" -eq 60 ]]; then
    echo "Timed out waiting for MySQL."
    exit 1
  fi

  sleep 2
done

php artisan migrate --force
