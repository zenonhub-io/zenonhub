<?php

declare(strict_types=1);

namespace Database\Seeders\Nom;

use App\Domains\Nom\Models\Pillar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PillarsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chain = load_chain();
        $pillars = [
            [
                'owner' => 'z1qzlaadsmar8pm0rdfwkctvxc8n2g5gaadxvmqj',
                'producer' => 'z1qrjdhy65zds69a96xlhheu4sy689k34x4hpse0',
                'withdraw' => 'z1qqwttth8sj5fchuqyr0ctum63hax2rqfyswk8y',
                'name' => 'SultanOfStaking',
            ], [
                'owner' => 'z1qrgr0e2u8y4pg4lzjr3fr62g8q4letyuntcvt5',
                'producer' => 'z1qrq3w9ulxapdqhw3vefe28r6gz94tm7k8qzqq4',
                'withdraw' => 'z1qrgr0e2u8y4pg4lzjr3fr62g8q4letyuntcvt5',
                'name' => 'tapwoot',
            ], [
                'owner' => 'z1qr7urykpjth3w9lcl66atgvu5fc0ywawzha220',
                'producer' => 'z1qzt4at0mp2wzrd5hj2pp47du5rgrwph9atmk07',
                'withdraw' => 'z1qpnrn0ejcn69qprekacqxsflt040fapjqj4y88',
                'name' => 'Zed',
            ], [
                'owner' => 'z1qre08vpz6uetpcsrrwkd4gucfg740pg2s0a4re',
                'producer' => 'z1qzzs6aju4vz4d8ndqch8kw8v8v97tcs9vhujvx',
                'withdraw' => 'z1qre08vpz6uetpcsrrwkd4gucfg740pg2s0a4re',
                'name' => 'Asgardians',
            ], [
                'owner' => 'z1qpt7v2rzcyuedrz9el5zcfmqx4pl3ra7ke4j2s',
                'producer' => 'z1qzt827p0fz3k7jdweechqz82gjnp8ux2pxfgju',
                'withdraw' => 'z1qpt7v2rzcyuedrz9el5zcfmqx4pl3ra7ke4j2s',
                'name' => 'WAGMI',
            ], [
                'owner' => 'z1qpwsd4stzqqjmeh59503xm5fz4y7z3knv7rngs',
                'producer' => 'z1qr6f4pmyycu44emt9t8cshkvlvqvdj7s22l6tw',
                'withdraw' => 'z1qpwsd4stzqqjmeh59503xm5fz4y7z3knv7rngs',
                'name' => 'Vopo',
            ], [
                'owner' => 'z1qpwjmq22d85csyqsn8q4fd3yn37amv5qazlr6x',
                'producer' => 'z1qzz3cm2mz4e0rm2j3w45klqrx4mslvwc55723c',
                'withdraw' => 'z1qztt98aghe7f9wca3gqx589ues79vzlz99mmvq',
                'name' => 'Elements',
            ], [
                'owner' => 'z1qzymmtmfr3gxz3fr80cq94rgaefzkvst4e90lz',
                'producer' => 'z1qr4w6ev7dwrusem859l7zg9ncqtp9tudk5829g',
                'withdraw' => 'z1qzymmtmfr3gxz3fr80cq94rgaefzkvst4e90lz',
                'name' => 'ZenonORG',
            ], [
                'owner' => 'z1qqvwzz2xq7q5gwk6uhcddgrpxlfcyzc8rsu82s',
                'producer' => 'z1qpza2k4fldpwsjrw0ae27ywnfnsc352sfed2e0',
                'withdraw' => 'z1qqvwzz2xq7q5gwk6uhcddgrpxlfcyzc8rsu82s',
                'name' => 'Mariposa01',
            ], [
                'owner' => 'z1qruqjjc6wc5m5ujq5s5hagft3t9gdfllg7z80q',
                'producer' => 'z1qph05gprn2wr5nacad2yq0yyfzqthxeau4clnl',
                'withdraw' => 'z1qruqjjc6wc5m5ujq5s5hagft3t9gdfllg7z80q',
                'name' => 'Octopos01',
            ], [
                'owner' => 'z1qpvejzz34rmd3gqlm7r6s54uwezj8p8ypfmrne',
                'producer' => 'z1qz2ejzxlngsncz7tu887eycykws2f7lxjhxu8e',
                'withdraw' => 'z1qpvejzz34rmd3gqlm7r6s54uwezj8p8ypfmrne',
                'name' => 'Zygonidz',
            ], [
                'owner' => 'z1qz87uj56v57y57pupnyyl36qdjp0ygyh62r5zz',
                'producer' => 'z1qq067tlxct049nsagknnj2yr0qh8a72elq2qrw',
                'withdraw' => 'z1qz87uj56v57y57pupnyyl36qdjp0ygyh62r5zz',
                'name' => 'Nomverse',
            ], [
                'owner' => 'z1qzvdd86z8emvc7fywrscpz5fn9zha6rfapx499',
                'producer' => 'z1qr472p2tn3cxs7ezale2c6ayplw4wqsj7uy4nz',
                'withdraw' => 'z1qzvdd86z8emvc7fywrscpz5fn9zha6rfapx499',
                'name' => 'Unizen',
            ], [
                'owner' => 'z1qz3n2ezt9uxk2zeus3sy550tyr8la4vhgfq48v',
                'producer' => 'z1qpecepafw0m6pyxd9lx0vngpqkrsd6f44gjx2v',
                'withdraw' => 'z1qz3n2ezt9uxk2zeus3sy550tyr8la4vhgfq48v',
                'name' => 'Zeus',
            ], [
                'owner' => 'z1qqwqy2c209ke7fweap3xagq0txphk94y7eg742',
                'producer' => 'z1qztkps274f430q7vtmpgws2r0t9jj7gmlywysm',
                'withdraw' => 'z1qqpqp6tjn0jjksmv09w5nx2scf7mst0vam85rg',
                'name' => 'CH405',
            ], [
                'owner' => 'z1qper3ncp6hzjy8t6jlc8wq2j7xcwvs5jl9mlsf',
                'producer' => 'z1qq4uqcn2jxy6m7lrru9d5qlrqde448q68ju5g5',
                'withdraw' => 'z1qzjsderuxmepuzqkep9dxp5d54gmtc4vwkdm89',
                'name' => 'Hybrid21',
            ], [
                'owner' => 'z1qppcdl9vu39wr2798nqk4mj5dwr42ercdpn94x',
                'producer' => 'z1qpdellhgna08mwpy5whawu43q78tfnref82c0q',
                'withdraw' => 'z1qppcdl9vu39wr2798nqk4mj5dwr42ercdpn94x',
                'name' => 'Inception',
            ], [
                'owner' => 'z1qzaghv5f8fuwz5sz80g34l9443w04e39e7s435',
                'producer' => 'z1qzjtve75fhcxwgv2k7gpyfk8gdr9qf24p9rpl8',
                'withdraw' => 'z1qzaghv5f8fuwz5sz80g34l9443w04e39e7s435',
                'name' => 'SPillar',
            ], [
                'owner' => 'z1qr3amdm99n7urvza4aqkyac0c4xyej37he6zn8',
                'producer' => 'z1qrhr43c5p4ad0kr0t5424qm4k3506mtyhtjjzl',
                'withdraw' => 'z1qz5hhpj290ljfvputdul9p8z0srclug6gm4zax',
                'name' => 'ChadassCapital',
            ], [
                'owner' => 'z1qpkrah3edmsm07zcx2w7jf7apmydau0r47spzx',
                'producer' => 'z1qpp4rwu6andy0rvc843p7eqfvqhsq9zqyjy7f4',
                'withdraw' => 'z1qpkrah3edmsm07zcx2w7jf7apmydau0r47spzx',
                'name' => 'AndastraCapital',
            ], [
                'owner' => 'z1qp3985rpm9cpj3rsp6swlv3zaan3whhcpftjz9',
                'producer' => 'z1qrpvrjrehc4lujjqh2q0dqfayvtg6098e5sscx',
                'withdraw' => 'z1qp3985rpm9cpj3rsp6swlv3zaan3whhcpftjz9',
                'name' => 'GenesisPillar',
            ], [
                'owner' => 'z1qp9psh8nkx6tese6z2s9nv6qg5r0gwaqutqnhg',
                'producer' => 'z1qz86uxgj2dawd0jlq6uz6ev843rt0wrd3emskq',
                'withdraw' => 'z1qp9psh8nkx6tese6z2s9nv6qg5r0gwaqutqnhg',
                'name' => 'Publius',
            ], [
                'owner' => 'z1qzfg9x830k0t0us9c4dpyla36je45vrj8wjlxx',
                'producer' => 'z1qqq3zt34xeapna4sccrewlws3lz4tpmw6d4yne',
                'withdraw' => 'z1qrsk5m3kewvvusg3s2hzte9pjdlm7al3wz709z',
                'name' => 'Zayin',
            ], [
                'owner' => 'z1qzuzrkc2h597yuaus997ukfvxj94ftenuvwddz',
                'producer' => 'z1qz2uf4dqzkpzy47sjhk8qy4wgq3jm7fcgk45da',
                'withdraw' => 'z1qzuzrkc2h597yuaus997ukfvxj94ftenuvwddz',
                'name' => 'bonsai',
            ], [
                'owner' => 'z1qqd3l99fevt8fjtx5k9v3y6639rkcznvls28j4',
                'producer' => 'z1qqy5p9rt7lr0hw59fl25t72s9w346848fhguhq',
                'withdraw' => 'z1qqd3l99fevt8fjtx5k9v3y6639rkcznvls28j4',
                'name' => 'starfruit',
            ], [
                'owner' => 'z1qpdekjkqdlgup2wwacg68pcccaufdm3h86ulzt',
                'producer' => 'z1qpdekjkqdlgup2wwacg68pcccaufdm3h86ulzt',
                'withdraw' => 'z1qpdekjkqdlgup2wwacg68pcccaufdm3h86ulzt',
                'name' => 'Barney',
            ],
        ];

        foreach ($pillars as $pillar) {
            Pillar::insert([
                'chain_id' => $chain->id,
                'owner_id' => load_account($pillar['owner'])->id,
                'producer_account_id' => load_account($pillar['producer'])->id,
                'withdraw_account_id' => load_account($pillar['withdraw'])->id,
                'name' => $pillar['name'],
                'slug' => Str::slug($pillar['name']),
                'qsr_burn' => 15000000000000,
                'weight' => 0,
                'produced_momentums' => 0,
                'expected_momentums' => 0,
                'missed_momentums' => 0,
                'momentum_rewards' => 0,
                'delegate_rewards' => 0,
                'az_engagement' => '0.00',
                'az_avg_vote_time' => null,
                'avg_momentums_produced' => 0,
                'total_momentums_produced' => 0,
                'is_legacy' => 1,
                'revoked_at' => null,
                'created_at' => '2021-11-24 12:00:00',
                'updated_at' => null,
            ]);
        }
    }
}
