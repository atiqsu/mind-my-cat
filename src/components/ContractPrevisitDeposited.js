import React, { useEffect, useState } from 'react';


const ContractPrevisitDeposited = (contractIdd, info, onUpdate) => {


    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    
    const handlePaymentNow = () => {

        setLoading(true);
        setError(null);

        Request.post(null, {
            action: 'mmc_process_previsit_deposit',
            contract_id: contractIdd,
            amount: info.amount || 100
        }).then(res => {

            if(res && res.pay_url) {

                window.location.href = res.pay_url;
            }

            setError('Something wrong!!...');
            
        }).catch(err => {

            setLoading(false);
            setError('Failed to fetch data. Please try again later.');
            console.log('in handleSave catch', err);

        }).finally(() => {
            setLoading(false);
        });

    }


	return (
		<div>

            <h3>Your contract is ready for initial previsit deposit of amount - {info.amount} </h3>

            {error && <p style={{color: 'red', fontWeight: 'bold', backgroundColor: 'yellow', padding: '10px'}}>{error}</p>} 
            {loading && <p style={{color: 'blue', fontWeight: 'bold', backgroundColor: 'lightyellow', padding: '10px'}}>Loading...</p>} 

            <button className='btn-submit' onClick={handlePaymentNow}> Deposit {info.amount} </button>

		</div>
	);
};


export default ContractPrevisitDeposited;

