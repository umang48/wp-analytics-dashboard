# Development Instructions

## Prerequisites
- Node.js (v14 or later)
- NPM

## Installation

1.  Navigate to the plugin directory in your terminal:
    ```bash
    cd wp-content/plugins/wp-analytics-dashboard
    ```
2.  Install dependencies:
    ```bash
    npm install
    ```
3.  Build the production assets:
    ```bash
    npm run build
    ```
    Or for development (watch mode):
    ```bash
    npm start
    ```

## Usage

Enable the plugin in WordPress admin. Go to the "Analytics" menu.
Choose between Database and File storage in the Settings tab.
The tracking script automatically runs on the frontend for all visitors.
