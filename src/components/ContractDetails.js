import React, { useEffect, useState, useCallback } from 'react';
import ContractReadyForDeposit from './ContractReadyForDeposit';
import Request from '../Request';


const ContractDetails = () => {

    const [contractIdd, setContractIdd] = useState(null);
    const [contractStatus, setContractStatus] = useState(null);
    const [ownerId, setOwnerId] = useState(0);
    const [sitterId, setSitterId] = useState(0);
    const [schedule, setSchedule] = useState({});
    const [serviceInfo, setServiceInfo] = useState({});
    const [info, setInfo] = useState({});

    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {

        const search = window.location.search;
        const urlParams = new URLSearchParams(search);
        const contractIdVal = urlParams.get('contract_id');

        if(contractIdVal) {
            setContractIdd(contractIdVal);
        }
      }, []
    );

    const fetchOnLoad = useCallback( async (contractId) => {
        
        setLoading(true);
        setError(null);

        Request.post(null, {
            action: 'mmc_get_contract_details',
            contract_id: contractId

        }).then(res => {

            if(res.contract) {
                setOwnerId(res.contract.owner_id);
                setSitterId(res.contract.sitter_id);
                setContractStatus(res.contract.status);
                setSchedule(res.contract.schedule);
                setServiceInfo(res.contract.service_info);

                let inf = {
                    amount: res.__fee || 100,
                    sitter: res.contract.sitter_id,
                    owner: res.contract.owner_id,
                    _stts: res.contract.status
                };

                setInfo({...inf});

                console.log('fffffff', inf, serviceInfo);

            } else {

                setError('Invalid info retrieved.');
            }


        }).catch(err => {

            setLoading(false);
            setError('Failed to fetch data. Please try again later.');

        }).finally(() => {
            setLoading(false);
        });

    }, []);


    useEffect(() => {

        if(contractIdd) {
            fetchOnLoad(contractIdd);
        }

      },[contractIdd]
    );


    const handleStatusUpdate = data => {

        //let newBookedSlot = [...data];

        //setBookedSlot(newBookedSlot);
    };



	return (
		<div>


			<h2>Contract Id: {contractIdd} & Status: {contractStatus}</h2>


            {contractStatus && contractStatus == 'ready_for_deposit' && (
                
                <ContractReadyForDeposit contractIdd={contractIdd} info={info} onStatusUpdate={handleStatusUpdate} />

            )}

		</div>
	);
};


export default ContractDetails;