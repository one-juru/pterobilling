import API from '@/common/utils/API'
import { UserInfo } from '@/typings'
import { login } from './redux/modules/user'
import React from 'react'
import { connect } from 'react-redux'
import { Route } from 'react-router'
import Footer from './components/Footer/Footer'
import NavBar from './components/NavBar'
import { GlobalState, setGlobal } from './redux/modules/global'
import MainRoutes from './routes'
import SideBar, { SidebarCategory, SideBarItem } from '@/common/component/SideBar'

const mapDispatchToProps = { login, setGlobal }

type AppProps = typeof mapDispatchToProps

class App extends React.Component<AppProps> {
  private ref: React.RefObject<SideBar> = React.createRef()

  public componentDidMount(): void {
    API.get<{ user: UserInfo }>('/user')
      .then((response) => {
        if (response.data.user) {
          this.props.login(response.data.user)
        }
      })
      .catch((error) => console.error(error))

    API.get<GlobalState>('/')
      .then((response) => {
        this.props.setGlobal(response.data)
      })
      .catch((error) => console.error(error))
  }

  public render(): JSX.Element {
    return (
      <>
        <NavBar
          onOpenSidebar={() => {
            this.ref.current?.toggleSidebar()
          }}
        />
        <div className="page-content">
          <SideBar ref={this.ref}>
            <SidebarCategory name="Hello">
              <SideBarItem icon="fa fas-user" to="/admin" exact>
                Users
              </SideBarItem>
              <SideBarItem to="/admin/lol" icon="fa fas-user">
                Users
              </SideBarItem>
            </SidebarCategory>
          </SideBar>
          <div className="container">
            <Route component={MainRoutes} />
          </div>
        </div>
        <Footer />
      </>
    )
  }
}

export default connect(undefined, mapDispatchToProps)(App)
