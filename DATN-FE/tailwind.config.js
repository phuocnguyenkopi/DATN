/** @type {import('tailwindcss').Config} */
export default {
  content: ["./index.html", "./src/**/*.{js,ts,jsx,tsx}"],
  theme: {
    extend: {
      colors: {
        primary: "#0a402b",
        secondary: "#1DAC6B",
        greenwhite: "#F1F3F6",
        tertiary: "#fbe249",
        redTe: "#F01414",
        yellcolor: "#f8f6ef",
        loading: "#e5e5e5",
        textgreen: "#0fb587",
        TextPrimary: "#c5ff54",
        price: "#1D9E34",
      },
      animation: {
        "bounce-slow": "bounce 2s infinite",
        swing: "swing 0.8s infinite both",
        jump: "jump 0.6s forwards",
      },
      keyframes: {
        swing: {
          "20%": { transform: "rotate(15deg)" },
          "40%": { transform: "rotate(-10deg)" },
          "60%": { transform: "rotate(5deg)" },
          "80%": { transform: "rotate(-5deg)" },
          "100%": { transform: "rotate(0deg)" },
        },
        jump: {
          // "0%, 100%": { bottom: "0" },
          // "50%": { bottom: "50px" },
          from: { right: "-100px" },
          to: { right: "2" },
        },
      },
      container: {
        center: true,
        padding: {
          DEFAULT: "1rem",
          sm: "3rem",
        },
      },
    },
  },

  plugins: [],
};
