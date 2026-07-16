CYAN='\033[0;36m'
LIGHT_CYAN='\033[1;36m'
BOLD='\033[1m'
NC='\033[0m'

echo ""
echo -e "${LIGHT_CYAN}  ____ _   _    _    ____ _____ _____ ____  ${NC}"
echo -e "${LIGHT_CYAN} / ___| | | |  / \  |  _ \_   _| ____|  _ \ ${NC}"
echo -e "${LIGHT_CYAN}| |   | |_| | / _ \ | |_) || | |  _| | |_) |${NC}"
echo -e "${LIGHT_CYAN}| |___|  _  |/ ___ \|  _ < | | | |___|  _ < ${NC}"
echo -e "${LIGHT_CYAN} \____|_| |_/_/   \_\_| \_\|_| |_____|_| \_\ for Laravel.${NC}"
echo ""

docker info > /dev/null 2>&1

# Ensure that Docker is running...
if [ $? -ne 0 ]; then
    echo "Docker is not running."
    echo "Please start Docker and try again."

    exit 1
fi

# Docker is used here only to scaffold the package. Laravel Sail Sail is not supported for package development.
echo -e "${CYAN}⚡ Docker is only used to create the package — Sail is not supported for package development.${NC}"
echo ""

docker run --rm \
    --pull=always \
    --user root \
    -e SHOW_WELCOME_MESSAGE=false \
    -e PHP_OPCACHE_ENABLE=1 \
    -e COMPOSER_HOME=/tmp/composer \
    -v "$(pwd)":/opt \
    -w /opt \
    serversideup/php:{!! $php !!}-cli \
    bash -c "
        apt-get update -qq >/dev/null 2>&1 && apt-get install -y -qq git >/dev/null 2>&1 && \
        composer global require laravel/installer --no-interaction --no-progress && \
        php /tmp/composer/vendor/bin/laravel package {!! $name !!} {!! $options !!} --no-interaction
    "

if [ ! -d "{!! $name !!}" ]; then
    echo ""
    echo -e "${BOLD}Package could not be created.${NC}"
    echo ""
    exit 1
fi

if [ ! -f "{!! $name !!}/composer.json" ]; then
    echo ""
    echo -e "${BOLD}Package may not have been created properly.${NC}"
    echo ""
fi

cd {!! $name !!}

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
echo -e "${CYAN}⚡ Docker is only used to create the package — Sail is not supported for package development.${NC}"
echo ""

if $SUDO -n true 2>/dev/null; then
    $SUDO chown -R $USER: .
    echo -e "${BOLD}Get started with:${NC} cd {!! $name !!}"
else
    echo -e "${BOLD}Please provide your password so we can make some final adjustments to your application's permissions.${NC}"
    echo ""
    $SUDO chown -R $USER: .
    echo ""
    echo -e "${BOLD}Thank you! We hope you build something incredible."
    echo -e "Dive in with:${NC} cd {!! $name !!}"
fi
