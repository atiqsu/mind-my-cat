import React from 'react';
import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';
import SitterHiring from './components/SitterHiring';
import ContractDetails from './components/ContractDetails';
//import SearchFilters from './components/SearchFilter';

import './style.scss';



domReady(() => {

    // const searchFiltersRoot = createRoot(
    //     document.getElementById( 'search-filters-root' )
    // );

    // const searchResultsRoot = createRoot(
    //     document.getElementById( 'search-results-root' )
    // );
    
	let sitterDomRoot = document.getElementById( 'hiring-sitter-root' );
	let contractDomRoot = document.getElementById( 'contract-details-root' );

	if ( sitterDomRoot ) {
		const hiringSitterRoot = createRoot( sitterDomRoot );
		hiringSitterRoot.render( <SitterHiring /> );
	}

    if ( contractDomRoot ) {
		const contractDetailsRoot = createRoot( contractDomRoot );
		contractDetailsRoot.render( <ContractDetails /> );
	}
    

    //searchFiltersRoot.render( <SearchFilters /> );
    //hiringSitterRoot.render( <SitterHiring /> );
});

