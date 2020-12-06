import { CreationDetail } from './creation-detail';

//Tables: No separate table. It will be used as internal key value.
export interface AddressInfoDTO extends CreationDetail{
    streetDetail: string,
    city: string,
    stateId: string,
    countryId: string,
    zipCode:string
}

export interface AddressInfo {
    id: string,
    addressInfo: AddressInfoDTO
}