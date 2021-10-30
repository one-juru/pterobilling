import { Action, ActionWithPayload, typedAction } from '@/common/redux/commonTypes'
import { UserInfo } from '@/typings'

export interface UserState {
  user?: UserInfo
  isLoggedIn: boolean
}

const initialState: UserState = {
  user: undefined,
  isLoggedIn: false,
}

export function login(u: UserInfo): ActionWithPayload<'user/LOGIN', typeof u> {
  return typedAction('user/LOGIN', u)
}
export function logout(): Action<'user/LOGOUT'> {
  return typedAction('user/LOGOUT')
}

type UserAction = ReturnType<typeof login | typeof logout>

export function userReducer(state = initialState, action: UserAction): UserState {
  switch (action.type) {
    case 'user/LOGIN':
      return {
        ...state,
        user: action.payload,
        isLoggedIn: true,
      }
    case 'user/LOGOUT':
      return {
        ...state,
        user: undefined,
        isLoggedIn: false,
      }
    default:
      return state
  }
}
