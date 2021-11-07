import React, { Component } from 'react'

interface CategoryProps {
  name: string
  children?: React.ReactNode
}

interface CategoryState {
  isOpen: boolean
}

class Category extends Component<CategoryProps, CategoryState> {
  public render(): JSX.Element {
    return (
      <div className="sidebar-category">
        <h5 className="sidebar-title">{this.props.name}</h5>
        <div className="category-content">{this.props.children}</div>
      </div>
    )
  }
}

export default Category
