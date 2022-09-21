<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemberController extends Controller
{

    public static $status = [
        "Anggota Baru",
        "Anggota",
        "Alumni",
    ];

    private $majors = [
        "Teknik Sipil" => [
            "Teknologi Rekayasa Konstruksi Jalan dan Jembatan",
            "Teknologi Konstruksi Bangunan Gedung",
            "Teknologi Konstruksi Bangunan Air",
            "Teknologi Konstruksi Jalan dan Jembatan",
        ],
        "Teknik Mesin" => [
            "Teknologi Rekayasa Manufaktur",
            "Teknologi Mesin",
            "Teknologi Industri",
            "Teknologi Rekayasa Pengelasan dan Fabrikasi",
        ],
        "Teknik Kimia" => [
            "Teknologi Kimia",
            "Teknologi Pengolahan Minyak dan Gas Bumi",
            "Teknologi Rekayasa Kimia Industri",
        ],
        "Teknik Elektro" => [
            "Teknologi Listrik",
            "Teknologi Rekayasa Pembangkit Energi",
            "Teknologi Rekayasa Jaringan Telekomunikasi",
            "Teknologi Rekayasa Instrumentasi dan Kontrol",
            "Teknologi Telekomunikasi",
            "Teknologi Elektronika",
        ],
        "Tata Niaga" => [
            "Akuntansi",
            "Administrasi Bisnis",
            "Perbankan dan Keuangan",
            "Akuntasi Lembaga Keuangan Syariah",
        ],
        "Teknologi Informasi Dan Komputer" => [
            "Teknik Informatika",
            "Teknik Rekayasa Komputer Jaringan",
            "Teknik Rekayasa Multi Media",
        ],
    ];

    public function index(Request $request) {
        $members = $this->getMembers($request);
        $page = 1;
        $perPage = 10;
        $maxPage = ceil($members->count()/$perPage);
        // dd($maxPage);

        if (is_numeric($request->page)) {
            $page = $request->page;
        }

        $data['members'] = $members;
        $data['status'] = self::$status;
        $data['page'] = $page;
        $data['perPage'] = $perPage;
        $data['maxPage'] = $maxPage;
        return view('admin.pages.member.index', $data);
    }

    public function store(Request $request) {
        $this->validate($request, [
            "nim" => "required|unique:members,nim",
            "name" => "required",
        ]);
        $member = new Member();
        $member->name = strtolower($request->name);
        $member->nim = $request->nim;
        $member->save();

        return redirect()->route('member.edit',['id' => $member->id])->with('success', 'Anggota berhasil ditambahkan');
    }

    public function edit($id) {
        $member = Member::find($id);
        if ($member) {
            $data['member'] = $member;
            $majors = json_encode($this->majors);
            $data['majors'] = $majors;
            $data['status'] = self::$status;
            $data['image'] = ($member->profile_picture) ? Source::find($member->profile_picture) : '';
            
            return view('admin.pages.member.edit', $data);
        }
        return redirect()->route('member');
    }

    public function update(Request $request, $id) {
        $this->validate($request, [
            "nim" => "required|unique:members,nim,".$id,
            "name" => "required",
            "address" => "required",
            "phone_number" => "required",
            "email" => "required|email",
            "major" => "required",
            "study_program" => "required",
            "interested_in" => "required",
            "profile_picture" => "required|exists:sources,id",
            "born_at" => "required",
            "birth_place" => "required",
            "joined_at" => "required",
            "status" => "required",
        ]);

        $member = Member::find($id);
        if ($member) {
            $member->nim = $request->nim;
            $member->profile_picture = $request->profile_picture;
            $member->name = strtolower($request->name);
            $member->address = strtolower($request->address);
            $member->phone_number = $request->phone_number;
            $member->email = strtolower($request->email);
            $member->major = strtolower($request->major);
            $member->study_program = strtolower($request->study_program);
            $member->interested_in = strtolower($request->interested_in);
            $member->born_at = strtolower($request->born_at);
            $member->birth_place = $request->birth_place;
            $member->joined_at = $request->joined_at;
            $member->status = $request->status;
            $member->save();
            return redirect()->route('member.edit', ['id' => $member->id])->with('success', 'Perubahan berhasil disimpan!');
        }

        return redirect()->route('member')->with('failed', 'Anggota tidak ditemukan!');
    }

    public function destroy($id) {
        $member = Member::find($id);
        if ($member) {
            $temp = $member;
            $member->delete();
            return redirect()->route('member')->with('success', 'Anggota dengan nama '.$temp->name.' berhasil dihapus');
        }
        return redirect()->route('member')->with('error', 'Anggota tidak ditemukan');
    }

    public function newMember(Request $request) {
        
        $this->validate($request, [
            "nim" => "required|unique:members,nim",
            "name" => "required",
            "address" => "required",
            "phone_number" => "required",
            "email" => "required|email",
            "major" => "required",
            "study_program" => "required",
            "interested_in" => "required",
            "profile_picture" => "required|exists:sources,id",
            "born_at" => "required",
            "birth_place" => "required"
        ]);

        $member = new Member();
        $member->nim = $request->nim;
        $member->profile_picture = $request->profile_picture;
        $member->name = strtolower($request->name);
        $member->address = strtolower($request->address);
        $member->phone_number = $request->phone_number;
        $member->email = strtolower($request->email);
        $member->major = strtolower($request->major);
        $member->study_program = strtolower($request->study_program);
        $member->interested_in = strtolower($request->interested_in);
        $member->born_at = strtolower($request->born_at);
        $member->birth_place = $request->birth_place;
        $member->joined_at = date('Y');
        $member->save();

        return redirect()->route('main.recruitment.success')->with('success', 'Anda berhasil mengdaftar');
    }



    public function getMembers(Request $request) {

        $data['members'] = [];
        $members = Member::whereIn('status', [0,1,2]);

        if ($request->status) {
            $status = '';
            foreach (self::$status as $k => $v) if ($request->status == strtolower($v)) $status = $k;
            $members = Member::where('status', $status);
        }

        if ($request->search) {
            $members = $members->where('name', 'like', '%'. $request->search . '%')
            ->orWhere('nim', 'like', '%'. $request->search . '%');
        }

        return $members->orderBy('name')->get();
    }
}
