import React from 'react';
import PropTypes from 'prop-types';
import LinkifyIt from 'linkify-it';
import tlds from 'tlds';
import { objectForEach } from 'utils/objects';

const linkify = new LinkifyIt();
linkify.tlds(tlds);

class Linkify extends React.Component {
  static propTypes = {
    className:  PropTypes.string,
    properties: PropTypes.object
  };

  static defaultProps = {
    className:  '',
    properties: {}
  };

  constructor(props) {
    super(props);
    this.parseCounter = 0;
  }

  /**
   *
   * @param {*} match
   * @param {number} idx
   * @returns {*}
   */
  createLink = (match, idx) => {
    const props = {
      href: match.url,
      key:  `parse${this.parseCounter}match${idx}`
    };
    objectForEach(this.props.properties, (val, key) => {
      props[key] = val;
    });

    return React.createElement(
      'a',
      props,
      match.text
    );
  };

  /**
   *
   * @param {*} match
   * @param {number} idx
   * @returns {*}
   */
  createImg = (match, idx) => {
    const props = {
      src: match.url,
      key: `parse${this.parseCounter}match${idx}`
    };
    objectForEach(this.props.properties, (val, key) => {
      props[key] = val;
    });

    match.text = React.createElement(
      'img',
      props
    );
    return this.createLink(match, idx);
  };

  /**
   *
   * @param {string} string
   * @returns {*}
   */
  parseString = (string) => {
    const elements = [];
    if (string === '') {
      return elements;
    }

    const matches = linkify.match(string);
    if (!matches) {
      return string;
    }

    let lastIndex = 0;
    matches.forEach((match, idx) => {
      // Push the preceding text if there is any
      if (match.index > lastIndex) {
        elements.push(string.substring(lastIndex, match.index));
      }
      if (match.text.toLowerCase().match(/\.(jpeg|jpg|gif|png)$/) !== null) {
        elements.push(this.createImg(match, idx));
      } else {
        elements.push(this.createLink(match, idx));
      }

      lastIndex = match.lastIndex;
    });

    if (lastIndex < string.length) {
      elements.push(string.substring(lastIndex));
    }

    return (elements.length === 1) ? elements[0] : elements;
  };

  /**
   *
   * @param {*} children
   * @returns {*}
   */
  parse = (children) => {
    let parsed = children;

    if (typeof children === 'string') {
      parsed = this.parseString(children);
    } else if (React.isValidElement(children) && (children.type !== 'a') && (children.type !== 'button')) {
      parsed = React.cloneElement(
        children,
        {
          key: `parse${this.parseCounter += 1}`
        },
        this.parse(children.props.children)
      );
    } else if (Array.isArray(children)) {
      parsed = children.map((child) => {
        return this.parse(child);
      });
    }

    return parsed;
  };

  render() {
    this.parseCounter = 0;
    const parsedChildren = this.parse(this.props.children);

    return <span className={this.props.className}>{parsedChildren}</span>;
  }
}

export default Linkify;
