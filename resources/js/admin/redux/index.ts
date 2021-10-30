import { applyMiddleware, combineReducers, compose, createStore } from 'redux'
import { createLogger } from 'redux-logger'
import thunk from 'redux-thunk'
import { globalReducer } from '@/admin/redux/modules/global'
import { userReducer } from '@/admin/redux/modules/user'

// Creating the Redux logger
const logger = createLogger()

// Applying modules
export const rootReducer = combineReducers({
  global: globalReducer,
  user: userReducer,
})

/*
 * Setup the compose enhancer:
 * - if we are in production mode, use the redux composer
 * - else, use the redux devtool composer if present
 */
let composeEnhancer: typeof compose
if (process.env.NODE_ENV !== 'production' && window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__) {
  composeEnhancer = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__
} else {
  composeEnhancer = compose
}

export type RootState = ReturnType<typeof rootReducer>

// Configure redux midlewares depending on the NODE_ENV
const middlewares = process.env.NODE_ENV !== 'development' ? [thunk] : [thunk, logger]

// Create the store and export it
const store = createStore(rootReducer, composeEnhancer(applyMiddleware(...middlewares)))
export default store
