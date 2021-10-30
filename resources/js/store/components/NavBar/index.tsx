import React from 'react'
import { NavLink } from 'react-router-dom'
import { CombinedState } from 'redux'
import { RootState } from '@/store/redux'
import { connect } from 'react-redux'
import { withTranslation, I18nextProviderProps } from 'react-i18next'
import classNames from 'classnames'
import { RouteComponentProps, withRouter } from 'react-router'
import { UnregisterCallback } from 'history'
import { logout } from '@/store/redux/modules/user'
import API from '@/common/utils/API'

const mapStateToProps = (state: RootState): CombinedState<RootState> => state
const mapDispatchToProps = { logout }
type NavbarProps = ReturnType<typeof mapStateToProps> &
  I18nextProviderProps &
  RouteComponentProps &
  typeof mapDispatchToProps

interface NavbarState {
  mobileOpen: boolean
  submenu: string
}

class Navbar extends React.Component<NavbarProps, NavbarState> {
  public state: NavbarState = {
    mobileOpen: false,
    submenu: '',
  }

  public constructor(props: NavbarProps) {
    super(props)
    this.logout = this.logout.bind(this)
  }

  private unlisten: UnregisterCallback | undefined

  private openSub(sub: string): void {
    if (this.state.submenu === sub) {
      this.setState({ submenu: '' })
    } else {
      this.setState({ submenu: sub })
    }
  }

  private isSub(sub: string): boolean {
    return this.state.submenu === sub
  }

  private setLang(lang: string): void {
    if (lang != this.props.i18n.language) {
      this.props.i18n.changeLanguage(lang)
    }
  }

  private logout(): void {
    API.delete('/user')
      .then(() => {
        this.props.logout()
        this.props.history.push('/')
      })
      .catch((error) => console.error(error))
  }

  public componentDidMount(): void {
    this.unlisten = this.props.history.listen(() => {
      this.setState({
        mobileOpen: false,
        submenu: '',
      })
    })
  }

  public componentWillUnmount(): void {
    if (this.unlisten) {
      this.unlisten()
    }
  }

  public render(): JSX.Element {
    const i18n = this.props.i18n

    return (
      <nav className="navbar">
        <div className="container">
          <div className="navbar-brand">
            <NavLink to="/" className="navbar-item logo" activeClassName="is-active">
              <img src={this.props.global.appIcon} alt={`${this.props.global.appName}'s Logo`} />
              <span className="brand-text">{this.props.global.appName}</span>
            </NavLink>

            <button
              role="button"
              className="navbar-burger"
              data-target="nav-menu"
              onClick={() => this.setState({ mobileOpen: !this.state.mobileOpen })}
            >
              <i className="fas fa-bars"></i>
            </button>
          </div>

          <div
            id="nav-menu"
            className={classNames('navbar-menu', { 'is-active': this.state.mobileOpen })}
          >
            <div className="navbar-start">
              <div className="navbar-item has-dropdown is-hoverable">
                <button className="navbar-link" onClick={() => this.openSub('plans')}>
                  {i18n.t('store:components.navbar.plans')}
                </button>

                <div
                  className={classNames('navbar-dropdown', {
                    'is-active': this.isSub('plans'),
                  })}
                >
                  <NavLink to="/plans" className="navbar-item" activeClassName="is-active">
                    {i18n.t('store:components.navbar.all_plans')}
                  </NavLink>
                  <hr className="navbar-divider" />
                  {this.props.global.plans.map((plan, index) => (
                    <NavLink
                      to={`/plans/${plan.id}`}
                      className="navbar-item"
                      key={index}
                      activeClassName="is-active"
                    >
                      {plan.name}
                    </NavLink>
                  ))}
                </div>
              </div>

              <NavLink to="/contact" className="navbar-item" activeClassName="is-active">
                {i18n.t('store:components.navbar.contact')}
              </NavLink>
              <NavLink to="/support" className="navbar-item" activeClassName="is-active">
                {i18n.t('store:components.navbar.support')}
              </NavLink>
            </div>

            <div className="navbar-end">
              <div className="navbar-item has-dropdown is-hoverable">
                <button className="navbar-link" onClick={() => this.openSub('currency')}>
                  {i18n.t('store:components.navbar.currency')}
                </button>
                <div
                  className={classNames('navbar-dropdown', {
                    'is-active': this.isSub('currency'),
                  })}
                ></div>
              </div>

              <div className="navbar-item has-dropdown is-hoverable">
                <button className="navbar-link" onClick={() => this.openSub('languages')}>
                  {i18n.t(`langs.${i18n.language}`)}
                </button>

                <div
                  className={classNames('navbar-dropdown', {
                    'is-active': this.isSub('languages'),
                  })}
                >
                  {this.props.i18n.languages.map((language, index) => (
                    <button
                      className="navbar-item"
                      key={index}
                      onClick={() => this.setLang(language)}
                    >
                      {i18n.t(`langs.${language}`)}
                    </button>
                  ))}
                </div>
              </div>

              {this.props.user.isLoggedIn ? (
                <div className="navbar-item has-dropdown is-hoverable">
                  <button className="navbar-link" onClick={() => this.openSub('account')}>
                    {this.props.user.user?.email}
                  </button>
                  <div
                    className={classNames('navbar-dropdown', {
                      'is-active': this.isSub('account'),
                    })}
                  >
                    <a href="/my" className="navbar-item">
                      {i18n.t('store:components.navbar.client')}
                    </a>
                    <a href="/my/credits" className="navbar-item">
                      {i18n.t('store:components.navbar.client-credits')}
                    </a>
                    <a href="/my/account" className="navbar-item">
                      {i18n.t('store:components.navbar.client-account')}
                    </a>

                    {this.props.user.user?.is_admin ? (
                      <a href="/admin" className="navbar-item">
                        {i18n.t('store:components.navbar.admin')}
                      </a>
                    ) : null}

                    <hr className="navbar-divider"></hr>
                    <button className="navbar-item" onClick={this.logout}>
                      {i18n.t('store:components.navbar.logout')}
                    </button>
                  </div>
                </div>
              ) : (
                <div className="navbar-item has-dropdown is-hoverable">
                  <button className="navbar-link" onClick={() => this.openSub('account')}>
                    {i18n.t('store:components.navbar.account')}
                  </button>
                  <div
                    className={classNames('navbar-dropdown', {
                      'is-active': this.isSub('account'),
                    })}
                  >
                    <NavLink to="/login" className="navbar-item" activeClassName="is-active">
                      <i className="fas fa-signin"></i>
                      {i18n.t('store:components.navbar.login')}
                    </NavLink>
                    <NavLink to="/register" className="navbar-item" activeClassName="is-active">
                      {i18n.t('store:components.navbar.register')}
                    </NavLink>
                    <hr className="navbar-divider" />
                    <NavLink
                      to="/forgot-password"
                      className="navbar-item"
                      activeClassName="is-active"
                    >
                      {i18n.t('store:components.navbar.forgot_password')}
                    </NavLink>
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>
      </nav>
    )
  }
}

export default withRouter(
  withTranslation('store')(connect(mapStateToProps, mapDispatchToProps)(Navbar))
)
