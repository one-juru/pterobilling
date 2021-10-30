/*
 * Global module store will allow us to share application data through the whole application
 */
import { ActionWithPayload, typedAction } from '@/common/redux/commonTypes'

export interface GlobalState {
  appName: string
  appIcon: string
  appVersion: string
  plans: {
    id: number
    name: string
  }[]
  currentRoute?: string
}

const initialState: GlobalState = {
  appName: 'PteroBilling',
  appIcon: '/images/icon.png',
  appVersion: '',
  plans: [],
}

export function setGlobal(
  state: GlobalState
): ActionWithPayload<'global/SET_GLOBAL', typeof state> {
  return typedAction('global/SET_GLOBAL', state)
}

export function setCurrentRouteName(
  routeName: string
): ActionWithPayload<'global/SET_CURRENT_ROUTE', typeof routeName> {
  return typedAction('global/SET_CURRENT_ROUTE', routeName)
}

type GlobalAction = ReturnType<typeof setGlobal | typeof setCurrentRouteName>

export function globalReducer(state = initialState, actions: GlobalAction): GlobalState {
  switch (actions.type) {
    case 'global/SET_GLOBAL':
      return {
        ...state,
        ...actions.payload,
      }

    case 'global/SET_CURRENT_ROUTE':
      state.currentRoute = actions.payload
      document.title = state.currentRoute + ' - ' + state.appName
      return state

    default:
      return state
  }
}
