docker info > /dev/null 2>&1

# Ensure that Docker is running...
if [ $? -ne 0 ]; then
    echo "Docker is not running."

    exit 1
fi

# Extract Node.js from the official Node image (needed for custom starter kits via npx)...
docker volume create node-binaries >/dev/null 2>&1
docker run --rm -v node-binaries:/out node:24-slim cp -a /usr/local/bin /usr/local/lib /out/

docker run --rm \
    --pull=always \
    --user root \
    -e SHOW_WELCOME_MESSAGE=false \
    -e COMPOSER_HOME=/tmp/composer \
    -v node-binaries:/usr/local/node:ro \
    -v "$(pwd)":/opt \
    -w /opt \
    serversideup/php:8.5-cli \
    bash -c "export PATH=/usr/local/node/bin:\$PATH && \
        apt-get update -qq && apt-get install -y -qq git && \
        composer global require laravel/installer --no-interaction --no-progress && \
        php /tmp/composer/vendor/bin/laravel new {{ name }} {{ options }} --no-interaction ; \
        cd {{ name }} && \
        ([ -f vendor/autoload.php ] || composer install --no-interaction --ignore-platform-reqs) && \
        php ./artisan sail:install --with={{ with }} --php={{ php }} {{ devcontainer }}"

docker volume rm node-binaries >/dev/null 2>&1

cd {{ name }}

# Allow build with no additional services..
if [ "{{ services }}" == "none" ]; then
    ./vendor/bin/sail build
else
    ./vendor/bin/sail pull {{ services }}
    ./vendor/bin/sail build
fi

CYAN='\033[0;36m'
LIGHT_CYAN='\033[1;36m'
BOLD='\033[1m'
NC='\033[0m'

echo ""

if command -v doas &>/dev/null; then
    SUDO="doas"
elif command -v sudo &>/dev/null; then
    SUDO="sudo"
else
    echo "Neither sudo nor doas is available. Exiting."
    exit 1
fi

if $SUDO -n true 2>/dev/null; then
    $SUDO chown -R $USER: .
    echo -e "${BOLD}Get started with:${NC} cd {{ name }} && ./vendor/bin/sail up && ./vendor/bin/sail npm install && ./vendor/bin/sail npm run dev"
else
    echo -e "${BOLD}Please provide your password so we can make some final adjustments to your application's permissions.${NC}"
    echo ""
    $SUDO chown -R $USER: .
    echo ""
    echo -e "${BOLD}Thank you! We hope you build something incredible. Dive in with:${NC} cd {{ name }} && ./vendor/bin/sail up && ./vendor/bin/sail npm install && ./vendor/bin/sail npm run dev"
fi
