import { Alpine, Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm';
import { registerTrailMap } from './trail-map';

registerTrailMap(Alpine);

Livewire.start();
