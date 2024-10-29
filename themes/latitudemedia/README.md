# Iot-electronics
Latitude Media WP theme

## First install
1. Add theme to WP installation
2. Install required plugins 
    - `Advanced Custom Fields PRO` - https://www.advancedcustomfields.com/pro/
3. Activate theme

## Setup and build assets

Node version: **22.8.0**

1. `npm i` - only first install
2. Run assets watcher **dev** mode:
    - `npm run watch`
3. Build assets **dev** mode:
    - `npm run build:assets`
4. Build assets **prod** mode: 
    - `npm run build:assets:prod`
5. Assets builds to the `dist` folder.