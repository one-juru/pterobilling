import '../common/bootstrap'

import React, { Suspense } from 'react'
import ReactDOM from 'react-dom'
import { BrowserRouter, Route } from 'react-router-dom'
import { Provider } from 'react-redux'
import '@/common/i18n'
import pluginStore from '@/common/plugins'

import App from './App'

import PageLoader from '@/common/component/PageLoader'
import store from './redux'
import { PluginProvider } from 'react-pluggable'

ReactDOM.render(
  <Provider store={store}>
    <Suspense fallback="Loading">
      <PluginProvider pluginStore={pluginStore}>
        <BrowserRouter>
          <Route component={App} />
        </BrowserRouter>
      </PluginProvider>
    </Suspense>
    <PageLoader needToLoad={true} />
  </Provider>,
  document.getElementById('app')
)
