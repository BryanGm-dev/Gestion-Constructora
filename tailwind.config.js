/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}"
  ],
  theme: {
    extend: {
      colors: {
        customBlue: '#141b25',  // Aquí defines el código hexadecimal del color personalizado
        customGreen: '#10B981'  // Puedes definir más colores personalizados
    },
  },
},
  plugins: [],
}

