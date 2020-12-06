import { CreationDetail } from './creation-detail';
import { State } from './state';

//Tables: CountryState
export interface CountryDTO extends CreationDetail{
    countryName: string,
    shortName: string,
    mobileCode: string,
    state: State[]
}

export interface Country {
    id: string,
    country: CountryDTO
}