const colors = require('tailwindcss/colors');

module.exports = {
  prefix: 'atw-',
  content: [
    './addons/styling/admin/options.php',
    './blocks/**/*.{php,js}',
    './includes/*.php',
    './templates/*.php',
    './templates/block-partials/*.php',
    './assets/js/*.js',
    './assets/css/*.css'
  ],
  theme: {
    extend: {},
    btn: {
      primary: {
        color: ({ opacityVariable, opacityValue }) => {
          if (opacityValue !== undefined) {
            return `rgba(var(--wpa-btn-primary-color), ${opacityValue})`
          }
          if (opacityVariable !== undefined) {
            return `rgba(var(--wpa-btn-primary-color), var(${opacityVariable}, 1))`
          }
          return `rgb(var(--wpa-btn-primary-color))`
        },
        background: ({ opacityVariable, opacityValue }) => {
          if (opacityValue !== undefined) {
            return `rgba(var(--wpa-btn-primary-background), ${opacityValue})`
          }
          if (opacityVariable !== undefined) {
            return `rgba(var(--wpa-btn-primary-background), var(${opacityVariable}, 1))`
          }
          return `rgb(var(--wpa-btn-primary-background))`
        },        
        border: ({ opacityVariable, opacityValue }) => {
          if (opacityValue !== undefined) {
            return `rgba(var(--wpa-btn-primary-border), ${opacityValue})`
          }
          if (opacityVariable !== undefined) {
            return `rgba(var(--wpa-btn-primary-border), var(${opacityVariable}, 1))`
          }
          return `rgb(var(--wpa-btn-primary-border))`
        },
      }
    },
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
      form: {
        gray: {
          50: ({ opacityVariable, opacityValue }) => {
            if (opacityValue !== undefined) {
              return `rgba(var(--wpa-color-gray-50), ${opacityValue})`
            }
            if (opacityVariable !== undefined) {
              return `rgba(var--wpa-color-gray-50), var(${opacityVariable}, 1))`
            }
            return `rgb(var(--wpa-color-gray-50))`
          },        
          100: ({ opacityVariable, opacityValue }) => {
            if (opacityValue !== undefined) {
              return `rgba(var(--wpa-color-gray-100), ${opacityValue})`
            }
            if (opacityVariable !== undefined) {
              return `rgba(var--wpa-color-gray-100), var(${opacityVariable}, 1))`
            }
            return `rgb(var(--wpa-color-gray-100))`
          },        
          200: ({ opacityVariable, opacityValue }) => {
            if (opacityValue !== undefined) {
              return `rgba(var(--wpa-color-gray-200), ${opacityValue})`
            }
            if (opacityVariable !== undefined) {
              return `rgba(var--wpa-color-gray-200), var(${opacityVariable}, 1))`
            }
            return `rgb(var(--wpa-color-gray-200))`
          },        
          300: ({ opacityVariable, opacityValue }) => {
            if (opacityValue !== undefined) {
              return `rgba(var(--wpa-color-gray-300), ${opacityValue})`
            }
            if (opacityVariable !== undefined) {
              return `rgba(var--wpa-color-gray-300), var(${opacityVariable}, 1))`
            }
            return `rgb(var(--wpa-color-gray-300))`
          },        
          400: ({ opacityVariable, opacityValue }) => {
            if (opacityValue !== undefined) {
              return `rgba(var(--wpa-color-gray-400), ${opacityValue})`
            }
            if (opacityVariable !== undefined) {
              return `rgba(var--wpa-color-gray-400), var(${opacityVariable}, 1))`
            }
            return `rgb(var(--wpa-color-gray-400))`
          },        
          500: ({ opacityVariable, opacityValue }) => {
            if (opacityValue !== undefined) {
              return `rgba(var(--wpa-color-gray-500), ${opacityValue})`
            }
            if (opacityVariable !== undefined) {
              return `rgba(var--wpa-color-gray-500), var(${opacityVariable}, 1))`
            }
            return `rgb(var(--wpa-color-gray-500))`
          },        
          600: ({ opacityVariable, opacityValue }) => {
            if (opacityValue !== undefined) {
              return `rgba(var(--wpa-color-gray-600), ${opacityValue})`
            }
            if (opacityVariable !== undefined) {
              return `rgba(var--wpa-color-gray-600), var(${opacityVariable}, 1))`
            }
            return `rgb(var(--wpa-color-gray-600))`
          },        
          700: ({ opacityVariable, opacityValue }) => {
            if (opacityValue !== undefined) {
              return `rgba(var(--wpa-color-gray-700), ${opacityValue})`
            }
            if (opacityVariable !== undefined) {
              return `rgba(var--wpa-color-gray-700), var(${opacityVariable}, 1))`
            }
            return `rgb(var(--wpa-color-gray-700))`
          },        
          800: ({ opacityVariable, opacityValue }) => {
            if (opacityValue !== undefined) {
              return `rgba(var(--wpa-color-gray-800), ${opacityValue})`
            }
            if (opacityVariable !== undefined) {
              return `rgba(var--wpa-color-gray-800), var(${opacityVariable}, 1))`
            }
            return `rgb(var(--wpa-color-gray-800))`
          },        
          900: ({ opacityVariable, opacityValue }) => {
            if (opacityValue !== undefined) {
              return `rgba(var(--wpa-color-gray-900), ${opacityValue})`
            }
            if (opacityVariable !== undefined) {
              return `rgba(var--wpa-color-gray-900), var(${opacityVariable}, 1))`
            }
            return `rgb(var(--wpa-color-gray-900))`
          }
        }
      },
      gray: colors.gray,
      indigo: colors.indigo,
      blue: colors.blue,
      red: colors.red,
      green: colors.green,
      yellow: colors.amber
    }
  },
  plugins: [],
  corePlugins: {
      preflight: false
  }
}
