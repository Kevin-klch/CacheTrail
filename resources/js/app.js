import { Alpine, Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm';
import { registerDirectoryMap, registerShareLocation, registerTrailMap } from './trail-map';

registerTrailMap(Alpine);
registerDirectoryMap(Alpine);
registerShareLocation(Alpine);

Livewire.start();
