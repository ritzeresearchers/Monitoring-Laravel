module.exports = {
    apps: [
      {
        name: 'laravel-queue',
        script: 'artisan',
        args: 'queue:work --tries=3',
        interpreter: 'php',
        interpreter_args: 'artisan',
        autorestart: true,
        watch: false,
        max_memory_restart: '1G',
        env: {
          NODE_ENV: 'production',
        },
      },
    ],
  };