<?php
/**
 *
 * License, TERMS and CONDITIONS
 *
 * This software is lisensed under the GNU LESSER GENERAL PUBLIC LICENSE (LGPL) version 3
 * Please read the license here : http://www.gnu.org/licenses/lgpl-3.0.txt
 *
 *  Redistribution and use in source and binary forms, with or without
 *  modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * ATTRIBUTION REQUIRED
 * 4. All web pages generated by the use of this software, or at least
 * 	  the page that lists the recent questions (usually home page) must include
 *    a link to the http://www.lampcms.com and text of the link must indicate that
 *    the website\'s Questions/Answers functionality is powered by lampcms.com
 *    An example of acceptable link would be "Powered by <a href="http://www.lampcms.com">LampCMS</a>"
 *    The location of the link is not important, it can be in the footer of the page
 *    but it must not be hidden by style attibutes
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR "AS IS" AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE FREEBSD PROJECT OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This product includes GeoLite data created by MaxMind,
 *  available from http://www.maxmind.com/
 *
 *
 * @author     Dmitri Snytkine <cms@lampcms.com>
 * @copyright  2005-2011 (or current year) ExamNotes.net inc.
 * @license    http://www.gnu.org/licenses/lgpl-3.0.txt GNU LESSER GENERAL PUBLIC LICENSE (LGPL) version 3
 * @link       http://www.lampcms.com   Lampcms.com project
 * @version    Release: @package_version@
 *
 *
 */


namespace Lampcms;

use \Lampcms\Acl\Acl;
use \Lampcms\Forms\Form;

/**
 *
 * This class generates extra HTML to be added
 * on the user profile page
 * If Viewer has necessary permissions to
 * change user's role and/or to shred_user
 *
 * @author admin
 *
 */
class Usertools
{

	/**
	 *
	 * Generates HTML with drop-down roles menu
	 * and a Shred button if current Viewer has necessary
	 * permissions
	 *
	 * @param Registry $oRegistry
	 * @param User $oUser use whose profile is being viewed now
	 * @return string html fragment with Form and button
	 */
	public static function getHtml(Registry $oRegistry, User $oUser){
		$oACL = new Acl();

		/*print_r($oACL);
		 exit;*/

		$options = '';
		$shredButton = '';
		$token = '';
		$uid = $oUser->getUid();

		$role = $oRegistry->Viewer->reload()->getRoleId();
		d('role: '.$role);

		if($oACL->isAllowed($role, null, 'change_user_role')){

			d('change_user_role is allowed');
			$userRole = $oUser->getRoleId();
			$roles = $oACL->getRegisteredRoles();
			$token = Form::generateToken();


			foreach($roles as $roleName => $val){
				$selected = ($roleName === $userRole) ? ' selected' : '';
				$options .= "\n".vsprintf('<option value="%1$s"%2$s>%1$s</option>', array($roleName, $selected));
			}
		}

		if($oACL->isAllowed($role, null, 'shred_user')){
			d('getting shred button');
			$shredButton = '<div class="fl cb"><input type="button" class="ajax btn_shred" value="Shred User" id="shred'.$uid.'"></div>';
		}

		if(empty($options) && empty($shredButton)){
			return '';
		}
		
		return \tplSelectrole::parse(array($token, $uid, $options, $shredButton), false);
	}
}
