/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        orange: {
          500: '#f94316ff', // now bg-orange-500 uses this
        },
      },
      fontFamily: {
        fjalla: ["'Fjalla One'", 'sans-serif'],
      },
    },
  },
  plugins: [],
}
