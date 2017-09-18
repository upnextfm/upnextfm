import React from 'react';

const AppBar = ({children}) => {
    return(
        <header style={{position:'static'}}>
            {children}
        </header>
    )
}

export default AppBar;