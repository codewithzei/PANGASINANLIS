/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./index.php",
    "./pages/**/*.php",      // Lahat ng PHP files sa pages folder
    "./components/**/*.php", // Lahat ng PHP files sa components folder
    "./src/**/*.js"          // Lahat ng JS files sa src folder
  ],
  theme: {
    extend: {
      fontFamily: {
        roboto: ['Roboto', 'sans-serif'],
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in',
        'fade-out': 'fadeOut 0.3s ease-out forwards',
      },
      keyframes: {
        fadeIn: {
          'from': { opacity: '0', transform: 'translateY(-20px)' },
          'to': { opacity: '1', transform: 'translateY(0)' },
        },
        fadeOut: {
          'from': { opacity: '1', transform: 'translateY(0)' },
          'to': { opacity: '0', transform: 'translateY(-20px)' },
        },
      },
      zIndex: {
        '9999': '9999',
      },
    },
  },
  plugins: [],
}