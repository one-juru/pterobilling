import React, { Component } from 'react'
import classname from 'classnames'

interface SideBarState {
  isOpen: boolean
}

class SideBar extends Component<unknown, SideBarState> {
  public state: SideBarState = {
    isOpen: false,
  }

  public render(): JSX.Element {
    return (
      <div className={classname('sidebar-container', { active: this.state.isOpen })}>
        <div className="sidebar-overlay" onClick={() => this.toggleSidebar()}></div>
        <div className="sidebar">
          <h1 className="test">salut</h1>
        </div>
      </div>
    )
  }

  public toggleSidebar(): void {
    this.setState({
      isOpen: !this.state.isOpen,
    })
  }
}

export default SideBar
export * from './Items'
