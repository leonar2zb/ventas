PLEASE TAKE A TIME TO READ IMPORTANT NOTES AT THE END OF THIS README.MD FILE:
Sales and product management App with Laravel and Filament

Files and folders that can be automatically generated — such as those inside the public directory created by Filament — have been excluded from the Git repository.

To regenerate them after deployment, run:

composer install
php artisan vendor:publish --tag=filament-assets
npm install
npm run build  # or 'vite build' depending on your setup

A database migration an seeding must be executed in order to generate users and roles. 
please run: php artisan migrate --seed
Default admin user will be manager@mycompany.com with password "password"

Prepare the filesystem for public folder access so that images can be served from storage/app/public.
Run the following command:
php artisan storage:link

⚠️ Note: On Windows, you may need to run your terminal as administrator for this command to work properly. If you encounter permission issues, manually create the symbolic link using:
cmd /c mklink /D public\storage storage\app\public