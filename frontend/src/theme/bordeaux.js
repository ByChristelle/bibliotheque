import { definePreset } from '@primevue/themes'
import Aura from '@primeuix/themes/aura'

const BordeauxPreset = definePreset(Aura, {
  semantic: {
    primary: {
      50:  '#fdf2f4',
      100: '#fce7eb',
      200: '#E8D6DB',
      300: '#B78493',
      400: '#a05070',
      500: '#8a2040',
      600: '#7A0026',
      700: '#5e001c',
      800: '#470015',
      900: '#30000e',
      950: '#1a0008',
    },
    colorScheme: {
      light: {
        surface: {
          0:   '#ffffff',
          50:  '#F8F6F6',
          100: '#ECECEC',
          200: '#E8D6DB',
          300: '#B78493',
          400: '#a05070',
          500: '#8a2040',
          600: '#7A0026',
          700: '#5e001c',
          800: '#470015',
          900: '#30000e',
          950: '#1a0008',
        },
      },
      dark: {
        surface: {
          0:   '#ffffff',
          50:  '#1a0008',
          100: '#30000e',
          200: '#470015',
          300: '#5e001c',
          400: '#7A0026',
          500: '#8a2040',
          600: '#a05070',
          700: '#B78493',
          800: '#E8D6DB',
          900: '#fce7eb',
          950: '#fdf2f4',
        },
      },
    },
  },
})

export default BordeauxPreset
