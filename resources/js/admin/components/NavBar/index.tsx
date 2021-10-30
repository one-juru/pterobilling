import React from 'react'
import { NavLink } from 'react-router-dom'
import { CombinedState } from 'redux'
import { RootState } from '@/admin/redux'
import { connect } from 'react-redux'
import { withTranslation, I18nextProviderProps } from 'react-i18next'
import classNames from 'classnames'
import { RouteComponentProps, withRouter } from 'react-router'
import { UnregisterCallback } from 'history'
import { logout } from '@/admin/redux/modules/user'
import API from '@/common/utils/API'

const mapStateToProps = (state: RootState): CombinedState<RootState> => state
const mapDispatchToProps = { logout }

interface NavbarProps {
  onOpenSidebar: () => void
}

type Props = ReturnType<typeof mapStateToProps> &
  I18nextProviderProps &
  RouteComponentProps &
  typeof mapDispatchToProps &
  NavbarProps

interface NavbarState {
  mobileOpen: boolean
  submenu: string
}

class Navbar extends React.Component<Props, NavbarState> {
  public state: NavbarState = {
    mobileOpen: false,
    submenu: '',
  }

  public constructor(props: Props) {
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
        this.props.history.replace('/')
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
            <button
              role="button"
              className="navbar-burger"
              onClick={() => this.props.onOpenSidebar()}
            >
              <i className="fas fa-bars"></i>
            </button>

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
              <i className="fas fa-user"></i>
            </button>
          </div>

          <div
            id="nav-menu"
            className={classNames('navbar-menu', { 'is-active': this.state.mobileOpen })}
          >
            <div className="navbar-start"></div>

            <div className="navbar-end">
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
                    {i18n.t('admin:components.navbar.client')}
                  </a>
                  <a href="/my/credits" className="navbar-item">
                    {i18n.t('admin:components.navbar.client-credits')}
                  </a>
                  <a href="/my/account" className="navbar-item">
                    {i18n.t('admin:components.navbar.client-account')}
                  </a>
                  <a href="/admin" className="navbar-item">
                    {i18n.t('admin:components.navbar.admin')}
                  </a>
                  <hr className="navbar-divider"></hr>
                  <button className="navbar-item" onClick={this.logout}>
                    {i18n.t('admin:components.navbar.logout')}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </nav>
    )
  }
}

export default withRouter(
  withTranslation('admin')(connect(mapStateToProps, mapDispatchToProps)(Navbar))
)
