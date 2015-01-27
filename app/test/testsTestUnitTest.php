<?php
namespace Test;
/**
 * Class UnitTest
 */
class UnitTest extends \UnitTestCase {



    public function testTestCase() {

        $this->assertEquals('works',
            'works',
            'This is OK'
        );
    }

    public function testCreateUser()
    {
        $u = new \User();

        $u->ID = 999999;
        $u->Name = "1";
        $u->Pass = sha1("123");
        $u->Type = "2";
        $r = $u->save();

        $this->assertFalse($r);

        $u->Name = "11111111111";
        $r = $u->save();
        $this->assertTrue($r);

        $r = $u->delete();

        $this->assertTrue($r);

        $u = \User::find(999999);
        $this->assertNotNull($u);

    }
}
