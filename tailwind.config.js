import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            screens: {
                // Customizing the 'md' breakpoint to 780px
                md: "780px",
            },
            fontFamily: {
                sans: ["Roboto", "sans-serif"],
            },
        },
    },

    plugins: [forms],
};
