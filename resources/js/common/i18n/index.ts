import i18n from 'i18next'
import { initReactI18next } from 'react-i18next'
import HttpApi from 'i18next-http-backend'
import LanguageDetector from 'i18next-browser-languagedetector'

i18n
  .use(HttpApi)
  .use(LanguageDetector)
  .use(initReactI18next)
  .init({
    // I18next options
    lng: 'en',
    fallbackLng: 'en',
    debug: process.env.NODE_ENV === 'development',
    compatibilityJSON: 'v3',
    interpolation: {
      escapeValue: false,
    },

    defaultNS: 'default',
    ns: ['default', 'store'],

    react: {
      useSuspense: true,
    },

    // Backend options
    backend: {
      loadPath: '/locales/{{ns}}/{{lng}}.json',
      allowMultiLoading: true,
    },
  })
