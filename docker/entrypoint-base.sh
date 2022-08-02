
set -eu

echo "► Starting Main Entrypoint..."

echo "► Starting Supervisor"
service supervisor start

exec tini -- "${@}"
