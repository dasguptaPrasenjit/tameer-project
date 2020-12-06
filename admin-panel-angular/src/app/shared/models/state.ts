import { CreationDetail } from './creation-detail';

//Tables: document type under CountryState
export interface StateDTO extends CreationDetail{
    stateName: string
}

export interface State {
    id: string,
    state: StateDTO
}