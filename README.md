# GiftWish Web Application

GiftWish is a Symfony-based web application designed to manage wishlists, gifts, and invitations for collaborative gift exchanges. This website was developped as part of the WEBAPP UE at IMT Atlantique.

## Features

- User registration and authentication.
- Create, edit, and share wishlists.
- Add items to wishlists with details like name, description, price, and URL.
- Upload proof of purchased gifts.
- Collaborate with other users or simply shere with them a wishlist.
- Admin dashboard for managing users and viewing statistics.

## Installation

Follow these steps to set up the project locally:

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd tp-symfony/projetUEG
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Set up environment variables:
   - Copy `.env` to `.env.local` and configure database credentials:
     ```
     DATABASE_URL="mysql://username:password@127.0.0.1:3306/app"
     ```
The username and password used in the project are:
- username: root
- password: root_PWD-iaw

4. Install front-end assets:
   ```bash
   npm run build
   ```

5. Start the Symfony server:
   ```bash
   symfony server:start
   ```

## Database Migration and Setup

To set up the database and apply migrations for the `app.sql` database:

1. Create the database:
   ```bash
   php bin/console doctrine:database:create
   ```

2. Import the app.sql file:
   ```bash
   mysql -u root -p app < app.sql
   ```
3. Verify the databse:
Check that the tables and data have been imported correctly by logging into your database:
   ```bash
   mysql -u username -p app
   ```

## Usage

- Access the application at `http://127.0.0.1:8000`.
- Register or log in to start creating and managing wishlists.
- Admin users can access the dashboard at `/admin`.

### Sharing a wishlist
To share a wishlist with other users, you can specify their usernames when creating the wishlist or editing t.

### Admin account
To log in with the admin account, use the following credentials:
- username: admin@gmail.com
- password: admin2025

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository.
2. Create a new branch for your feature or bugfix.
3. Commit your changes and push to your fork.
4. Submit a pull request.

## License

This project is licensed under the MIT License. See the `LICENSE` file for details.
