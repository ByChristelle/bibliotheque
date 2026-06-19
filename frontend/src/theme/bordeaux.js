import { definePreset } from '@primevue/themes'
import Aura from '@primeuix/themes/aura'

const BordeauxPreset = definePreset(Aura, {
  semantic: {
    primary: {
      50:  '#fdf2f4',
      100: '#fce7eb',
      200: '#f9d0d8',
      300: '#f4a8b8',
      400: '#ec7590',
      500: '#e04570',
      600: '#cc2755',
      700: '#ac1a43',
      800: '#8b1a3a',
      900: '#6b0f2a',
      950: '#4a0a1e',
    },
    colorScheme: {
      light: {
        surface: {
          0:   '#ffffff',
          50:  '#fdf2f4',
          100: '#fce7eb',
          200: '#f9d0d8',
          300: '#f4a8b8',
          400: '#ec7590',
          500: '#e04570',
          600: '#cc2755',
          700: '#ac1a43',
          800: '#8b1a3a',
          900: '#6b0f2a',
          950: '#4a0a1e',
        },
      },
      dark: {
        surface: {
          0:   '#ffffff',
          50:  '#2d0f1e',
          100: '#3d1428',
          200: '#4a1830',
          300: '#5c1f3c',
          400: '#7a2a50',
          500: '#ac1a43',
          600: '#cc2755',
          700: '#e04570',
          800: '#ec7590',
          900: '#f4a8b8',
          950: '#fce7eb',
        },
      },
    },
  },
})

export default BordeauxPreset
