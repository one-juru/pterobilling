import { RootState } from '@/admin/redux'
import { GlobalState } from '@/admin/redux/modules/global'
import React from 'react'
import { I18nextProviderProps, withTranslation, Trans } from 'react-i18next'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import { CombinedState } from 'redux'

const mapStateToProps = (state: RootState): CombinedState<GlobalState> => state.global

type FooterProps = ReturnType<typeof mapStateToProps> & I18nextProviderProps

class Footer extends React.Component<FooterProps> {
  public render(): JSX.Element {
    const { i18n } = this.props
    return (
      <footer className="footer">
        <div className="container">
          <div className="left">
            {i18n.t('admin:components.footer.copyright', {
              replace: {
                year: new Date().getFullYear(),
              },
            })}
            <Link to="/">{this.props.appName}</Link>
          </div>
          <div className="center">
            <Trans i18nKey="components.footer.powered" ns="store">
              Powered by <a href="https://github.com/pterobilling/pterobilling">Pterobilling</a>
            </Trans>
          </div>
          <div className="right">
            <Link to="/status">{i18n.t('admin:components.footer.status')}</Link> |
            <Link to="/terms">{i18n.t('admin:components.footer.terms')}</Link> |
            <Link to="/privacy">{i18n.t('admin:components.footer.privacy')}</Link>
          </div>
        </div>
      </footer>
    )
  }
}

export default withTranslation('admin')(connect(mapStateToProps)(Footer))
