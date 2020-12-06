import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { LayoutModule } from '@angular/cdk/layout';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatButtonModule } from '@angular/material/button';
import { MatSidenavModule } from '@angular/material/sidenav';
import { MatIconModule } from '@angular/material/icon';
import { MatListModule } from '@angular/material/list';
import { MatCardModule } from '@angular/material/card';
import { MatInputModule } from '@angular/material/input';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { MatMenuModule } from '@angular/material/menu';
import { MatGridListModule } from '@angular/material/grid-list';
import { MatTabsModule } from '@angular/material/tabs';
import { MatTableModule } from '@angular/material/table';
import { MatPaginatorModule } from '@angular/material/paginator';
import { MatProgressBarModule } from '@angular/material/progress-bar';
import { MatSnackBarModule } from '@angular/material/snack-bar';
import { MatDialogModule } from '@angular/material/dialog';
import { LayoutTopLeftComponent } from './components/layout-top-left/layout-top-left.component';
import { SignOutComponent } from './components/sign-out/sign-out.component';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatSlideToggleModule } from '@angular/material/slide-toggle';
import { MatStepperModule } from '@angular/material/stepper';
import { MatSelectModule } from '@angular/material/select';
import { MatExpansionModule } from '@angular/material/expansion';
import { MenuComponent } from './components/menu/menu.component';
import { MatChipsModule } from '@angular/material/chips';
import { MatTooltipModule } from '@angular/material/tooltip';
import { MatAutocompleteModule } from '@angular/material/autocomplete';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatBadgeModule } from '@angular/material/badge';

import { LayoutTopLeftSideContentComponent } from './components/layout-top-left-side-content/layout-top-left-side-content.component';
import { FileBrowserComponent } from './components/file-browser/file-browser.component';
import { ProgressComponent } from './components/progress/progress.component';
import { ModalActionComponent } from './components/modal-action/modal-action.component';
import { ModalBodyComponent } from './components/modal-body/modal-body.component';
import { ModalHeaderComponent } from './components/modal-header/modal-header.component';
import { TextTruncatePipe } from './pipes/text-truncate.pipe';
import { NoRecordsComponent } from './components/no-records/no-records.component';
import { ConfirmComponent } from './components/confirm/confirm.component';
import { ImgFallbackDirective } from './directives/img-fallback.directive';
import { AgmCoreModule } from '@agm/core';
import { environment } from 'src/environments/environment';
import { TrackCarrierComponent } from './components/track-carrier/track-carrier.component';
import { LocateCarrierComponent } from './components/track-carrier/locate-carrier/locate-carrier.component';
import { NotificationComponent } from './components/notification/notification.component';


const modules = [
    CommonModule,
    RouterModule,
    FormsModule,
    ReactiveFormsModule,
    LayoutModule,
    MatToolbarModule,
    MatButtonModule,
    MatSidenavModule,
    MatIconModule,
    MatListModule,
    MatCardModule,
    MatInputModule,
    MatButtonModule,
    MatMenuModule,
    MatGridListModule,
    MatTabsModule,
    MatTableModule,
    MatPaginatorModule,
    MatProgressBarModule,
    MatSnackBarModule,
    MatDialogModule,
    MatCheckboxModule,
    MatSlideToggleModule,
    MatSelectModule,
    MatStepperModule,
    MatExpansionModule,
    MatChipsModule,
    MatTooltipModule,
    MatProgressSpinnerModule,
    MatAutocompleteModule,
    MatBadgeModule
];

const components = [
    LayoutTopLeftComponent,
    LayoutTopLeftSideContentComponent,
    SignOutComponent,
    MenuComponent,
    FileBrowserComponent,
    ProgressComponent,
    ModalActionComponent,
    ModalBodyComponent,
    ModalHeaderComponent,
    TextTruncatePipe,
    NoRecordsComponent,
    ConfirmComponent,
    ImgFallbackDirective,
    TrackCarrierComponent,
    LocateCarrierComponent
];

@NgModule({
    declarations: [
        ...components,
        NotificationComponent
    ],
    imports: [
        ...modules,
        AgmCoreModule.forRoot({
            apiKey: environment.gmapKey,
            libraries: ["places", "geometry"]
        }),
    ],
    exports: [
        ...modules,
        AgmCoreModule,
        ...components
    ]
})
export class SharedModule {
    static forRoot() {
        return {
            ngModule: SharedModule,
            providers: []
        };
    }
}
