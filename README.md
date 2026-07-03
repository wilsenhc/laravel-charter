![](https://banners.beyondco.de/Charter%20for%20Laravel.png?theme=light&pattern=architect&style=style_1&description=A+visual+command+builder+for+Laravel+Sail&md=1&showWatermark=0&fontSize=100px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg)

# Charter for Laravel

Quickly scaffold a new Laravel Sail application with your preferred options through a visual interface. Pick your services, starter kit, and tools — then copy a single curl command to spin up your project.

## Instructions

### Requirements

- PHP 8.3+
- [Laravel](https://laravel.com)
- [Composer](https://getcomposer.org)
- [Bun](https://bun.sh)

### Setup

```bash
cp .env.example .env
composer install
php artisan key:generate
bun install
bun run build
```

### Development

```bash
composer run dev
```

### Testing

```bash
composer run test
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Security

If you discover a security vulnerability, please open an issue rather than posting publicly.

## Credits

- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
