import defaultTheme from "tailwindcss/defaultTheme";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            sans: [
                "HelveticaNeue-Light",
                "Helvetica Neue Light",
                "Helvetica Neue",
                "Helvetica",
                "Arial",
                "Lucida Grande",
                "sans-serif",
            ],
            colors: {
                customPrimaryColor: "#fcfcfc",
                customSecondaryColor: "#35408e",
            },
            fontSize: {
                smallFont: "10px",
                mediumFont: "12px",
                largeFont: "14px",
            },
        },
    },
    plugins: [],
};
