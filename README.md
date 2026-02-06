# Farlo Technical Developer Test - API Show Display

This project is a technical test demonstrating a WordPress setup that imports show data from the Official London Theatre API into a custom post type, using a small custom plugin and ACF for structured data. The data is then displayed on the home page.


## Requirements
- PHP 8.2+
- Composer
- Node.js
- DDEV
- Gulp


## Setup

1. Clone the repository
2. Run `composer install`
3. Run `ddev config`
4. Change the project name from the default to your chosen project name
5. Accept the remaining defaults unless you have a strong reason not to
6. Visit the site URL shown in wp-config-ddev.php
7. Complete the WordPress install
8. Activate the Farlo theme
9. Activate plugins
      - Advanced Custom fields
      - Farlo show importer


## Front-end build

Source files live in `src/assets/` and are compiled to `dist/`.

From the theme directory:

- `npm install`
  Install required Node modules

- `npm run build`
  Compiles the current setup and adds compliled files to `dist/`.

- `npm run watch`
  Compiles the current setup and adds compliled files to `dist/`, and keeps watching the files for more changes

### Note
I commited compiled assets to help with the reviewing


## Importing shows

1. Log into wp-admin
2. Navigate to **Tools → Import Shows**
3. Click **Run Import**
4. Wait for confirmation that import was successful

The importer should be idempotent and can be run multiple times without duplicating.


## Notes / Decisions

- Shows are imported into a custom post type (`shows`), to make it easier to display and manage the shows on the site
- Importing is triggered manually via an admin page rather than on page load, this was so it would not affect load time of the site
- Shows are stored as a shows Custom Post Type and show fields are stored in ACF. This was an effcient way to manage the API data. 
- Where multiple booking URLs are provided, we store only the first valid URL for simplicity.
- Idempotency is achived by using the show ID, to compare new feed to current feed and only add shows that don't exist otherwise update show only
- A plugin was used for the importer, to help keep the code clean, and allow us to create a Admin UI page to make using the tool easy for non tech skilled people if needed


## Possible improvements

- Schedule the importer via WP-Cron Job
- Improve error logging and reporting, log to the WP debug log
- Adding caching could be something to add, we could add transient caching and add a delay to the call, so it will only use the cached API call for 10mins from the last time it was called.
- Add a filter to the show grid to make it easier to navigate through the shows on the front end.
- Set shows that do not appear on the latest API to draft, so old shows do not show in grid 

## Repo Link
https://github.com/joshwb82/farlo-dev-test
