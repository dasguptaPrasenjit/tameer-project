import { Component, OnInit } from '@angular/core';
import { MenuItem } from '../../../shared/models/menu-item';
import { AuthService } from '../../../core/services/auth.service';
import { UserDTO } from '../../../shared/models/user';
import { UploaderService } from '../../../shared/services/api/uploader.service';
import { Observable } from 'rxjs/internal/Observable';

@Component({
    selector: 'app-account',
    templateUrl: './account.component.html',
    styleUrls: ['./account.component.scss']
})
export class AccountComponent implements OnInit {
    constructor(
        public _authService: AuthService,
        public _uploaderService: UploaderService
    ) { 
    }

    ngOnInit(): void {
    }

}
