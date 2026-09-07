<table class="table table-bordered table-sm">
  <thead class="thead-light">
    <tr>
      <th>Type</th>
      <th>Name</th>
      <th>Default / Reference Value</th>
      <th>Units</th>
      <th>Method</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    @forelse($params as $p)
      <tr>
        <td>
          <span class="badge badge-{{ $p->type == 'heading' ? 'secondary' : 'primary' }}">{{ ucfirst($p->type ?? 'parameter') }}</span>
        </td>
        <td>
          <input type="text" class="form-control form-control-sm" id="paramname_{{ $p->id }}" value="{{ $p->name }}">
        </td>
        <td>
          @if($p->type != 'heading')
            <input type="text" class="form-control form-control-sm" id="paramdefault_{{ $p->id }}" value="{{ $p->default_value }}">
          @endif
        </td>
        <td>
          @if($p->type != 'heading')
            <input type="text" class="form-control form-control-sm" id="paramunits_{{ $p->id }}" value="{{ $p->units }}">
          @endif
        </td>
        <td>
          @if($p->type != 'heading')
            <input type="text" class="form-control form-control-sm" id="parammethod_{{ $p->id }}" value="{{ $p->method }}">
          @endif
        </td>
        <td>
          <button type="button" class="btn btn-xs btn-success updatetestparameters" id="{{ $p->id }}"><i class="fas fa-save"></i> Save</button>
          <button type="button" class="btn btn-xs btn-danger deleteparameter" data-id="{{ $p->id }}"><i class="fas fa-trash"></i></button>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="6" class="text-center text-muted py-2">No parameters added for this test yet.</td>
      </tr>
    @endforelse
  </tbody>
</table>
