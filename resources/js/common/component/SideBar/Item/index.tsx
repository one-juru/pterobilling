import React, { Component } from 'react'
import { NavLink } from 'react-router-dom'

interface ItemProps {
  icon?: string
  to?: string
  children?: React.ReactNode
  exact?: boolean
}

class Item extends Component<ItemProps> {
  private showIcon(): JSX.Element | undefined {
    if (this.props.icon) {
      return <i className={this.props.icon} />
    }
  }

  public render(): JSX.Element {
    return (
      <NavLink to={this.props.to || ''} className="sidebar-item" exact={this.props.exact}>
        {this.showIcon()}
        {this.props.children}
      </NavLink>
    )
  }
}

export default Item
