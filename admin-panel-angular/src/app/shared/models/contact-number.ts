import { CreationDetail } from './creation-detail';

//Tables: No separate table. It will be used as internal key value.
export interface ContactNumberDTO extends CreationDetail{
    countryId: string, // to get mobile code from country
    mobileNo: string,
    alternateMobileNo?: string,
    landline?: string
}

export interface ContactNumber {
    id: string,
    contactNumber: ContactNumberDTO
}