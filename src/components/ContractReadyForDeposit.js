import React, { useEffect, useState } from 'react';
import Request from '../Request';


const ContractReadyForDeposit = (contractIdd, info, onStatusUpdate) => {

    const [amount, setAmount] = useState(0);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    

    useEffect(() => {

        setAmount(info.amount);

    }, [info]);

  
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

    

    console.log('received...', info);


	return (
		<div>

            <h3>Your contract is ready for initial previsit deposit of amount - 100 </h3>

            {error && <p style={{color: 'red', fontWeight: 'bold', backgroundColor: 'yellow', padding: '10px'}}>{error}</p>} 
            {loading && <p style={{color: 'blue', fontWeight: 'bold', backgroundColor: 'lightyellow', padding: '10px'}}>Loading...</p>} 

            <button className='btn-submit' onClick={handlePaymentNow}> Deposit 100 </button>

		</div>
	);
};


export default ContractReadyForDeposit;

