@csrf
<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label">Kode</label>
    <input name="code" class="form-control @error('code') is-invalid @enderror"
           value="{{ old('code', $playstation->code ?? '') }}" required>
    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
  <div class="col-md-4">
    <label class="form-label">Nama</label>
    <input name="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $playstation->name ?? '') }}">
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
  <div class="col-md-4">
    <label class="form-label">Harga per Jam (Rp)</label>
    <input type="number" step="0.01" min="0"
           name="price_per_hour"
           class="form-control @error('price_per_hour') is-invalid @enderror"
           value="{{ old('price_per_hour', $playstation->price_per_hour ?? 0) }}" required>
    @error('price_per_hour') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-4">
    <label class="form-label">Status</label>
    <select name="status" class="form-select @error('status') is-invalid @enderror">
      @foreach(['available'=>'Available','maintenance'=>'Maintenance'] as $val=>$label)
        <option value="{{ $val }}" @selected(old('status', $playstation->status ?? 'available')==$val)>
          {{ $label }}
        </option>
      @endforeach
    </select>
    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
</div>

<div class="mt-3">
  <button class="btn btn-primary">Simpan</button>
  <a href="{{ route('playstations.index') }}" class="btn btn-light">Batal</a>
</div>
