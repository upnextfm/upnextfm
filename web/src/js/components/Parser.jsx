import React from 'react';
import LinkifyIt from 'linkify-it';
import tlds from 'tlds';

const REGEXPS = {
  colors: /\[#([a-fA-F0-9]{6})\](.*?)\[\/#\]/g,
  markdown: [
    [/_([^_]+)_/g, 'em'],
    [/\*\*([^_]+)\*\*/g, 'strong'],
    [/\*([^_]+)\*/g, 'em'],
    [/`([^_]+)`/g, 'code']
  ]
};
const linkify = new LinkifyIt();
linkify.tlds(tlds);

class Parser extends React.PureComponent {
  /**
   * @param {string} string
   * @returns {string}
   */
  parseColors = (string) => {
    return string.replace(REGEXPS.colors, (all, color, text) => {
      return `<span style="color: #${color}">${text}</span>`;
    });
  };

  /**
   * @param {string} string
   * @returns {string}
   */
  parseMarkdown = (string) => {
    let parsed = string;
    REGEXPS.markdown.forEach((markdown) => {
      parsed = parsed.replace(markdown[0], (all, text) => {
        return `<${markdown[1]}>${text}</${markdown[1]}>`;
      });
    });

    return parsed;
  };

  /**
   * @param {string} string
   * @returns {string}
   */
  parseLinks = (string) => {
    const matches = linkify.match(string);
    if (matches) {
      matches.forEach((match) => {
        if (match.text.toLowerCase().match(/\.(jpeg|jpg|gif|png)$/) !== null) {
          string = string.replace(
            match.text,
            `<a href="${match.text}" target="_blank"><img src="${match.text}" /></a>`
          );
        } else {
          string = string.replace(
            match.text,
            `<a href="${match.text}" target="_blank">${match.text}</a>`
          );
        }
      });
    }

    return string;
  };

  /**
   * @param {*} string
   * @returns {*}
   */
  parse = (string) => {
    let parsed = string;
    parsed = this.parseColors(parsed);
    parsed = this.parseMarkdown(parsed);
    parsed = this.parseLinks(parsed);

    return { __html: parsed };
  };

  render() {
    const { children, ...props } = this.props;

    return (
      <span
        {...props}
        dangerouslySetInnerHTML={this.parse(children)}
      />
    );
  }
}

export default Parser;
