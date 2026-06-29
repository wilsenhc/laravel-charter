CYAN='\033[0;36m'
LIGHT_CYAN='\033[1;36m'
BOLD='\033[1m'
NC='\033[0m'

echo ""
echo -e "${LIGHT_CYAN}  ____ _   _    _    ____ _____ _____ ____  ${NC}"
echo -e "${LIGHT_CYAN} / ___| | | |  / \  |  _ \_   _| ____|  _ \ ${NC}"
echo -e "${LIGHT_CYAN}| |   | |_| | / _ \ | |_) || | |  _| | |_) |${NC}"
echo -e "${LIGHT_CYAN}| |___|  _  |/ ___ \|  _ < | | | |___|  _ < ${NC}"
echo -e "${LIGHT_CYAN} \____|_| |_/_/   \_\_| \_\|_| |_____|_| \_\ ${NC}"
echo ""

docker info > /dev/null 2>&1

# Ensure that Docker is running...
if [ $? -ne 0 ]; then
    echo "Docker is not running."

    exit 1
fi

{{ node_setup }}
docker run --rm \
    --pull=always \
    --user root \
    -e SHOW_WELCOME_MESSAGE=false \
    -e COMPOSER_HOME=/tmp/composer \
{{ node_mount }}    -v "$(pwd)":/opt \
    -w /opt \
    serversideup/php:8.5-cli \
    bash -c "{{ node_path }}apt-get update -qq && apt-get install -y -qq git && \
        composer global require laravel/installer --no-interaction --no-progress && \
        php /tmp/composer/vendor/bin/laravel new {{ name }} {{ options }} --no-interaction ; \
        cd {{ name }} && \
        ([ -f vendor/autoload.php ] || composer install --no-interaction --ignore-platform-reqs) && \
        php ./artisan sail:install --with={{ with }} --php={{ php }} {{ devcontainer }}"

{{ node_cleanup }}

if [ ! -d "{{ name }}" ]; then
    echo ""
    echo -e "${BOLD}Project could not be created.${NC}"
    echo "The application may not be supported and installation may fail unexpectedly."
    echo ""
    exit 1
fi

if [ ! -f "{{ name }}/artisan" ]; then
    echo ""
    echo -e "${BOLD}Project may not have been created properly.${NC}"
    echo "The application may not be supported and installation may fail unexpectedly."
    echo ""
fi

cd {{ name }}

# Allow build with no additional services..
if [ "{{ services }}" == "none" ]; then
    ./vendor/bin/sail build
else
    ./vendor/bin/sail pull {{ services }}
    ./vendor/bin/sail build
fi

echo ""

if command -v doas &>/dev/null; then
    SUDO="doas"
elif command -v sudo &>/dev/null; then
    SUDO="sudo"
else
    echo "Neither sudo nor doas is available. Exiting."
    exit 1
fi

echo ""
echo -e "${LIGHT_CYAN}  ____ _   _    _    ____ _____ _____ ____  ${NC}"
echo -e "${LIGHT_CYAN} / ___| | | |  / \  |  _ \_   _| ____|  _ \ ${NC}"
echo -e "${LIGHT_CYAN}| |   | |_| | / _ \ | |_) || | |  _| | |_) |${NC}"
echo -e "${LIGHT_CYAN}| |___|  _  |/ ___ \|  _ < | | | |___|  _ < ${NC}"
echo -e "${LIGHT_CYAN} \____|_| |_/_/   \_\_| \_\|_| |_____|_| \_\ for Laravel.${NC}"
echo ""
echo -e "${CYAN}Enjoying Charter for Laravel? Consider supporting development:${NC}"
echo -e "${BOLD}https://paypal.me/wilsenjhc${NC}"
echo ""

if $SUDO -n true 2>/dev/null; then
    $SUDO chown -R $USER: .
    echo -e "${BOLD}Get started with:${NC} cd {{ name }} && ./vendor/bin/sail up && ./vendor/bin/sail npm install && ./vendor/bin/sail npm run dev"
else
    echo -e "${BOLD}Please provide your password so we can make some final adjustments to your application's permissions.${NC}"
    echo ""
    $SUDO chown -R $USER: .
    echo ""
    echo -e "${BOLD}Thank you! We hope you build something incredible."
    echo -e "Dive in with:${NC} cd {{ name }} && ./vendor/bin/sail up && ./vendor/bin/sail npm install && ./vendor/bin/sail npm run dev"
fi
