import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
    name: 'textTruncate'
})
export class TextTruncatePipe implements PipeTransform {

    transform(value: string, limit: number, trail: string = '..'): string {
        return value.length < limit
            ? value
            : value.slice(0, limit) + trail;
    }

}
