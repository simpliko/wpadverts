const colors = require('tailwindcss/colors');

module.exports = {
  prefix: 'atw-',
  purge: [],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {},
    colors: {
      primary: {
          main: ({ opacityVariable, opacityValue }) => {
            if (opacityValue !== undefined) {
              return `rgba(var(--wpa-color-primary-main), ${opacityValue})`
            }
            if (opacityVariable !== undefined) {
              return `rgba(var(--wpa-color-primary-main), var(${opacityVariable}, 1))`
            }
            return `rgb(var(--wpa-color-primary-main))`
          },
          light: ({ opacityVariable, opacityValue }) => {
            if (opacityValue !== undefined) {
              return `rgba(var(--wpa-color-primary-light), ${opacityValue})`
            }
            if (opacityVariable !== undefined) {
              return `rgba(var(--wpa-color-primary-light), var(${opacityVariable}, 1))`
            }
            return `rgb(var(--wpa-color-primary-light))`
          },
          dark: ({ opacityVariable, opacityValue }) => {
            if (opacityValue !== undefined) {
              return `rgba(var(--wpa-color-primary-dark), ${opacityValue})`
            }
            if (opacityVariable !== undefined) {
              return `rgba(var(--wpa-color-primary-dark), var(${opacityVariable}, 1))`
            }
            return `rgb(var(--wpa-color-primary-dark))`
          }
        
      },
      secondary: 'var(--wpa-color-secondary)',
      transparent: 'transparent',
      black: colors.black,
      white: colors.white,
      gray: colors.coolGray,
      indigo: colors.indigo,
      blue: colors.blue,
      red: colors.red
    }
  },
  variants: {
    extend: {},
  },
  plugins: [],
  corePlugins: {
      preflight: false
  }
}
