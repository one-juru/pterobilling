import React from 'react'
import { connect } from 'react-redux'
import { Route, RouteComponentProps, Switch } from 'react-router-dom'
import { CombinedState } from 'redux'
import { RootState } from '../redux'
import { GlobalState } from '../redux/modules/global'
import { withPlugins, WithPluginsProps } from 'react-pluggable'
import { PluginCustomRoute } from '@/typings'

/*
 * Routes
 */

const mapStateToProps = (state: RootState): CombinedState<GlobalState> => state.global

const mapDispatchToProps = {}

type AppRouterProps = ReturnType<typeof mapStateToProps> &
  typeof mapDispatchToProps &
  WithPluginsProps

class AppRouter extends React.Component<AppRouterProps & RouteComponentProps> {
  private customRoutes: PluginCustomRoute[] = []

  public componentDidMount(): void {
    this.customRoutes = this.props.pluginStore.executeFunction('plugins:custom-routes')
  }

  public render(): JSX.Element {
    return (
      <Switch>
        <Route exact path="/" component={undefined} />
        <Route path="*" component={undefined} />
      </Switch>
    )
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(withPlugins(AppRouter))
