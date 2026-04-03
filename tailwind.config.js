import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", ...defaultTheme.fontFamily.sans],
                display: ["Outfit", ...defaultTheme.fontFamily.sans],
                serif: ["Playfair Display", ...defaultTheme.fontFamily.serif],
                heading: ["Montserrat", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                dark: {
                    50: "#f8f9fa",
                    100: "#e9ecef",
                    200: "#dee2e6",
                    300: "#adb5bd",
                    400: "#6c757d",
                    500: "#495057",
                    600: "#343a40",
                    700: "#212529",
                    800: "#1a1d21",
                    900: "#121416",
                    950: "#0a0b0d",
                },
                accent: {
                    50: "#fff7ed",
                    100: "#ffedd5",
                    200: "#fed7aa",
                    300: "#fdba74",
                    400: "#fb923c",
                    500: "#f97316",
                    600: "#ea580c",
                    700: "#c2410c",
                    800: "#9a3412",
                    900: "#7c2d12",
                },
            },
        },
    },

    plugins: [
        forms,
        require("@tailwindcss/typography"),
    ],
};
